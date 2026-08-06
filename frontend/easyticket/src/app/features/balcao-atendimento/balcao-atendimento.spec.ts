import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BalcaoAtendimento } from './balcao-atendimento';

describe('BalcaoAtendimento', () => {
  let component: BalcaoAtendimento;
  let fixture: ComponentFixture<BalcaoAtendimento>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [BalcaoAtendimento],
    }).compileComponents();

    fixture = TestBed.createComponent(BalcaoAtendimento);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
